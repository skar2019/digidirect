define([
    'jquery',
    './dist/common/component'
], function ($, Component) {
    'use strict';

    $.widget('ewave.faq', {
        options: {
            url: '',
            baseUrl: '',
            multipleCollapsible: true,
            container: '#faq-container',
            searchForm: '#faq-form',
            searchField: '#faq-form .input',
            actionLinks: '[data-role="faq-item"]',
            items: '.faq-item',
            list: '[data-role="faq-listing"]',
            questions: '.faq-question',
            backItem: '.faq-back',
            toggleTags: '.tags-toggle',
            extraTags: '.faq-tags .tag.-extra',
            nextPage: '.faq-toolbar .pager a',
            compactModeBreakpoint: '768px',
            viewCustom: '',
            questionContainer: '[data-role="question-container"]',
            questionForm: '[data-role="question-form"]',
            questionButton: '[data-role="question-button"]',
            questionFormSubmit: '[data-role="question-submit"]'
        },

        _create: function () {
            new Component(this.options);
        }
    });

    return $.ewave.faq;
});
