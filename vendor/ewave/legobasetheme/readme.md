
Abstract parent base theme ([Wiki link](https://wiki.ewave.com/display/LEGO/Lego+Base+theme)) is a set of flexible, independent and scalable solutions, which are used in the development of modules and the child themes.

##SVG
[Wiki link](https://wiki.ewave.com/display/LEGO/Custom+Fonts)

##Custom Fonts
[Wiki link](https://wiki.ewave.com/display/LEGO/Quick+start%3A+SVG)

## Collapsible widget extend
Option | Type | Default | Description
------ | ---- | ------- | -----------
closeOnClickOutside | boolean | false | Specifies if the content(drop-down) is closed on mouse click outside the drop-down.
closeOnClickInside | boolean | false | Specifies if the content(drop-down) is closed on mouse click inside the drop-down.
toggleHeaderText | boolean | false | Specifies if the header text is changed on mouse click on the content(drop-down) items.
toggleHeaderActive | string | '-active' | Selector for header to set active class name after toggle container.
headerTextContainer | string | '[data-role="title"]' | Selector for element to replace text, searched for using .find() on the main collapsible element. If the element with the specified selector is not found on the main collapsible element, the header will use.
newHeaderText | string | '[data-header]' | Selector for element to get replacement text on mouse click on the content(drop-down) items, searched for using .find() on the main collapsible element. If the element with the specified selector is not found on the main collapsible element, the innerText of item will use.
toggleContainer | boolean | false | Specifies if the container is changed on mouse click on the header items ('toggleContainerAction' selector).
toggleContainerActive | string | '-active' | Selector for container to set active class name after click on the 'toggleContainerAction' selector.
toggleContainerAction | string | '[data-header]' | Selector for element to toggle container active class name.

##Back to top settings

Option | Type | Default | Description
------ | ---- | ------- | -----------
scrollStartClass | string | '-scroll' | Class which would be added on scroll event
scrollUpStateClass | string | '-up' | Class which would be added when page is scrolled up
scrollDownStateClass | string | '-down' | Class which would be added when page is scrolled down 
scrollIndent | number | 100 | The distance from which the logic starts 
alwaysVisible | bool | false | if true logic with class changing on scroll (up and down) wouldn't be applyed
scrollBodyTo | number | 0 | The value up to which page will be scrolled
