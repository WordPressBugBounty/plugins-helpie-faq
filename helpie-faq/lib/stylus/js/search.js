var Tracker=require("../../../assets/js/components/insights/tracker"),ObjectSearch=require("../../../assets/js/components/faq/object_search.js"),FaqFunctions=require("../../../assets/js/components/faq/functions.js"),FaqCollections=require("../../../assets/js/components/faq/collections.js"),Pagination=require("../../../assets/js/components/faq/pagination.js"),Mark=require("../../../node_modules/mark.js/dist/mark.js");const{error}=require("jquery");var selectors={accordions:".helpie-faq.accordions",faqSearch:".helpie-faq .search__wrapper .search__input",accordion:".accordion",accordionShow:"accordion--show",accordionHide:"accordion--hide",accordionHeading:".accordion__heading",accordionHeadingShow:"accordion__heading--show",accordionHeadingHide:"accordion__heading--hide",accordionHeader:".accordion .accordion__item .accordion__header",accordionItem:".accordion__item",accordionItemShow:"accordion__item--show",accordionItemHide:"accordion__item--hide",accordionBody:".accordion__body",searchMessage:".search .search__message",searchMessageContent:"<p class='search__message__content'>"+faqStrings.noFaqsFound+"</p>"},Stylus={searchTerm:"",typingTimer:0,doneTypingInterval:2e3,paginationEnabled:"0",highlightEnabled:!!helpie_faq_object.enable_search_highlight&&helpie_faq_object.enable_search_highlight,normalizeText:function(e){return e.normalize("NFD").replace(/[\u0300-\u036f]/g,"").toLowerCase()},setSearchAttr:function(){jQuery(selectors.accordionHeader).each((function(){var e=Stylus.normalizeText(jQuery(this).text());jQuery(this).attr("data-search-term",e)}))},showAllMatchedContent:function(e){1==this.highlightEnabled&&e.addClass("faq-search-matched")},resetShowingContent:function(){jQuery(".accordions .accordion__body").removeClass("faq-search-matched")},isContentMatch:function(e,o){var c=jQuery(e).find(selectors.accordionBody),r=Stylus.normalizeText(c.text());return this.showAllMatchedContent(c),r.indexOf(Stylus.normalizeText(o))>=0},resetHighlight:function(){new Mark(document.querySelector(".accordion"),{element:"span",className:"helpie-mark"}).unmark()},highlightText:function(e,o){if(0==this.highlightEnabled)return;let c={element:"span",className:"helpie-mark"};var r=new Mark(e);r.unmark({done:function(){r.mark(o,c)}})},isTitleMatch:function(e,o){var c=jQuery(e).find(".accordion__header").attr("data-search-term");return null!=c&&"undefined"!=c&&c.search(Stylus.normalizeText(o))>=0},isCategoryHeadingMatch:function(e,o){return Stylus.normalizeText(jQuery(e).prev(selectors.accordionHeading).text()).indexOf(Stylus.normalizeText(o))>=0},isCategroryAccordionMatch:function(e,o){if(0==jQuery(e).hasClass("accordion__category"))return!1;return this.isTitleMatch(e,o)},searchByAccordionItem:function(e,o){let c=this;searchTerm=c.searchTerm;let r=c.isTitleMatch(e,searchTerm),t=c.isContentMatch(e,searchTerm),s=c.searchByTags(e,searchTerm);var n=!!(r||t||s);return c.displayAccordionItem(e,n),n},onSearchKeyup:function(e){var o=this;const c=Stylus.normalizeText(o.searchTerm);jQuery(e).closest(selectors.accordions).children(selectors.accordion).each((function(){var e=jQuery(this),r=!1,t=o.isCategoryHeadingMatch(e,c);let s=e.get(0);o.highlightText(s,c),1==t?(r=!0,o.showAccordionSection(e,r)):e.find(".helpie-faq-col").children("ul").children(selectors.accordionItem).each((function(){$accordionItem=jQuery(this);var e=$accordionItem.hasClass("accordion__category");let t=o.searchByCategory($accordionItem,c),s=!(!e||!t),n=0==s,a=!1;n&&(a=o.searchInnerAccordionsItems($accordionItem,c));let i=0==e,d=!1;i&&(d=o.searchByAccordionItem($accordionItem,{})),e&&0==a&&o.showCategoryWithAccordions($accordionItem,s),n&&a&&o.showAccordionBelongsToCategory($accordionItem,!0),0==r&&(1==s&&(r=!0),1==n&&1==a&&(r=!0),1==i&&1==d&&(r=!0))})),o.displayHeading(e,r),o.showAccordion(e,r)}))},searchByCategory:function(e,o){return this.isCategroryAccordionMatch(e,o)},searchInnerAccordionsItems:function(e,o){let c=this,r=!1;return e.find(selectors.accordionItem).each((function(){$item=jQuery(this),1==c.searchByAccordionItem($item,{})&&(r=!0)})),r},init:function(){var e=this;e.setSearchAttr(),jQuery("body").on("keyup",selectors.faqSearch,(function(o){var c=jQuery(this).val().toLowerCase().replace(/[*+?^${}()|[\]\\]/gi,"");e.searchTerm=c;let r=FaqFunctions.getElements(this);return e.paginationEnabled=FaqFunctions.paginationEnabled(this),e.canSeeEmptyFAQsBlock(this,"hide"),""==e.searchTerm&&"1"==e.paginationEnabled?(jQuery(r.root).attr("data-search","0"),e.showAllAccordionsFromObject(this),!1):""==e.searchTerm&&"1"!=e.paginationEnabled?(e.showAllAccordions(this),e.resetHighlight(this),e.resetShowingContent(this),!1):("1"==e.paginationEnabled?(jQuery(r.root).attr("data-search","1"),ObjectSearch.init(this,c)):e.onSearchKeyup(this),e.showEmptyFAQsContent(this),""!=e.searchTerm&&(clearTimeout(e.typingTimer),void(e.typingTimer=setTimeout((function(){Tracker.searchCounter(e.searchTerm)}),e.doneTypingInterval))))}))},showAllAccordions:function(e){var o=this;jQuery(e).closest(selectors.accordions).children(selectors.accordion).each((function(){let e=jQuery(this);o.showAccordion(e,true),o.displayHeading(e,true),e.find(selectors.accordion).removeClass(selectors.accordionHide).addClass(selectors.accordionShow),e.find(selectors.accordionItem).removeClass(selectors.accordionItemHide).addClass(selectors.accordionItemShow)}))},showEmptyFAQsContent:function(e){var o=this,c=0,r=jQuery(e).closest(selectors.accordions).find(selectors.accordionItem).length;jQuery(e).closest(selectors.accordions).find(selectors.accordionItem).each((function(){0==jQuery(this).is(":visible")&&(c=parseInt(c)+1)})),c==r&&(jQuery(e).closest(selectors.accordions).find(selectors.accordion).each((function(){let e=jQuery(this);o.displayHeading(e,!1),o.showAccordion(e,!1)})),jQuery(e).closest(selectors.accordions).find(selectors.searchMessage).empty().show().append(selectors.searchMessageContent))},canSeeEmptyFAQsBlock:function(e,o){var c="none";"show"==o&&(c="block"),jQuery(e).closest(selectors.accordions).find(selectors.searchMessage).css("display",c)},displayAccordionItem:function(e,o){const c=1==o?selectors.accordionItemShow:selectors.accordionItemHide,r=0==o?selectors.accordionItemShow:selectors.accordionItemHide;e.removeClass(r).addClass(c)},displayHeading:function(e,o){const c=1==o?selectors.accordionHeadingShow:selectors.accordionHeadingHide,r=0==o?selectors.accordionHeadingShow:selectors.accordionHeadingHide;e.prev(selectors.accordionHeading).removeClass(r).addClass(c)},showCategoryAccordions:function(e,o){const c=1==o?selectors.accordionItemShow:selectors.accordionItemHide,r=0==o?selectors.accordionItemShow:selectors.accordionItemHide;jQuery(e).find(selectors.accordionItem).removeClass(r).addClass(c)},showAccordionSection:function(e,o){var c=this;c.displayHeading(e,o),c.showCategoryAccordions(e,o),c.showAccordion(e,o)},showAccordion:function(e,o){const c=1==o?selectors.accordionShow:selectors.accordionHide,r=0==o?selectors.accordionShow:selectors.accordionHide;jQuery(e).removeClass(r).addClass(c)},showCategoryWithAccordions:function(e,o){var c=this;c.displayAccordionItem(e,o),c.showCategoryAccordions(e,o),c.showAccordion(e,o)},showAccordionBelongsToCategory:function(e,o){jQuery(e).find(selectors.accordion).removeClass(selectors.accordionHide).addClass(selectors.accordionShow),this.displayAccordionItem(e,o)},searchByTags:function(e,o){let c=jQuery(e).find(".accordion__header").attr("data-tags"),r=!1;return null==c||"undefined"==c||0==c.length||c.split(",").forEach((function(e){-1!=(e=Stylus.normalizeText(e)).search(Stylus.normalizeText(o))&&(r=!0)})),r},showAllAccordionsFromObject:function(e){let o=FaqFunctions.getElements(e),c=FaqFunctions.getPaginationCurrentPage(o),r=FaqFunctions.getShortcodeIndex(o),t={page:c},s=FaqCollections.getCurrentShortcodeViewProps(r),n=FaqCollections.getTotalNoOfPages(s),a=FaqCollections.getCurrentPageViewProps(t,{collection:s.collection,items:s.items});FaqFunctions.appendFaqsContent(o,a),Pagination.renderPageLinks(o.pagination,{current:c,last:n})}};module.exports=Stylus;