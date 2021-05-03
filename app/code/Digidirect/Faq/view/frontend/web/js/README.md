## FAQ Settings

Option | Type | Default | Description
------ | ---- | ------- | -----------
url | string | '' | URL to load collection of the topics
baseUrl | string | '' | Base URL of FAQ page (admin setting "FAQ page URL")
multipleCollapsible | boolean | true | Defines if multiple FAQ topics can be expanded at the same time.
container | string | '#faq-container' | The area with FAQ categories and FAQ topics
searchForm | string | '#faq-form' | Search form selector
searchField | string |  '#faq-form .input' | Search input field selector
actionLinks | string |  '[data-role="faq-item"]' | Link selectors to load FAQ topics
items | string | '.faq-item' | The containers which contain question and answer
list | string | '[data-role="faq-listing"]' | List of FAQ topics
questions | string | '.faq-question' | List of questions
backItem | string | '.faq-back' | Back link to categories. Especially for Compact Mode
toggleTags | string | '.tags-toggle' | Switcher the display of hidden extra tags
extraTags | string | '.faq-tags .tag.-extra' | List of hidden extra tags
nextPage | string | '.faq-toolbar .pager a' | Links of FAQ toolbar pagination
compactModeBreakpoint | string | '768px' | Defines when should be enable Compact Mode. Use '' to disable Compact Mode logic
viewCustom | string |  '' | Extends core View
questionContainer | string | '[data-role="question-container"]' | Wrapper question form
questionForm | string |  '[data-role="question-form"]' | Selector question form
questionButton | string |  '[data-role="question-button"]' | Selector event button for show question form
questionFormSubmit | string | '[data-role="question-submit"]' | Selector button submint of question form
