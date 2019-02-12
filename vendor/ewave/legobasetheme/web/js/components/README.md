## WCAG-HANDLER

 Option | Type | Default | Description
 ------ | ---- | ------- | -----------
 focusElement | string | '.link' | Selector focus element
 focusOpenedClass | string | '-onfocus-open' | Class name for showing hidden child element
 expandedAttribute | string | 'aria-expanded' | WCAG attribute for opening element,
 expandedElement | string | '[aria-expanded]' | The selector for the opening element
 parentElement | string | '.-parent' | Selector for element which has child level 
 keyUp | number | 38 | Key number for button arrow up
 keyDown | number | 40 | Key number for button arrow down
 keyLeft | number | 37 | Key number for button arrow left
 keyRigt | number | 39 | Key number for button arrow right
 keyEsc | number | 27 | Key number for button Esc
 keyTab | number | 9 | Key number for button Tab
 minScreenWidth | screen | '768px' | Minimum screen width for desktop version
 
The widget can be initialized for any html element with drop-down elements. The element which shows hidden content must be HTML element that can to have focus (tags ```<a> or <button>```). For example:
```
<div class="dropdown-element -parent" data-mage-init='{"wcagHandler": {"focusElement": ".my-link"}}'>
    <button class="dropdown-button my-link" aria-expanded="false">Dropdown label</button>
    <ul class="dropdown-content">
        <li class="item -parent">
            <a class="my-link" aria-expanded="false">Item 1</a>
            <div class="hidden-content">
                <a class="my-link" href="">Link One</a>
                <a class="my-link" href="">Link Two</a>
            </div>
        </li>
        <li class="item">
            <a class="my-link">Item 2</a>
        </li>
        <li class="item">
            <a class="my-link">Item 3</a>
        </li>
    </ul>
</div>
```