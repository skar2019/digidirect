import ko from 'knockout';
import $ from 'jquery';

/** function initializing custom bindings */
export default function bindings(options, action, viewCore) {

    /**
     * Add class "-active" to necessary node
     * @type {{update: (function())}}
     */
    ko.bindingHandlers.activeElement = {
        update: (element, valueAccessor) => {
            let value = valueAccessor(),
                target,
                accestors;
            if (value) {
                target = element;
                $(element).addClass('-active');
                accestors = $(element).closest('.-level1').closest(options.itemClass);
                $(accestors.last()).addClass('-active');
            }
        }
    };

    /**
     * Adds class "-level'i'" to nodes, where 'i' is nesting level
     * @type {{update: (function())}}
     */
    ko.bindingHandlers.nestLevel = {
        update: (element, valueAccessor) => {
            let value = valueAccessor(),
                string = `-level${value}`;
            $(element).addClass(string);
        }
    };

    /**
     * Toggles the additional link appearence for click action
     * @type {{update: (function())}}
     */
    ko.bindingHandlers.toggleLink = {
        update: (element, valueAccessor, allBindings, viewModel) => {
            let [action,childrenCount] = valueAccessor(),
                menu = $(element).siblings().find(options.innerListsClass);
            if (viewModel.static) {
                menu = menu[0];
            }
            if (!!childrenCount && action === 'click') {
                viewCore.addlink(element, menu, options);
            }
            if (action === 'hover') {
                $(menu).find('.-added').remove();
            }
        }
    };

    /**
     * Adds custom options to node
     * @type {{update: (function())}}
     */
    ko.bindingHandlers.customOption = {
        update: (element, valueAccessor) => {
            let value = valueAccessor();
            viewCore.customOptions(element, value);
        }
    };

    /**
     * Dynamicaly chose what node action is used and provide action binding
     * @type {{update: (function()), preprocess: (function())}}
     */
    ko.bindingHandlers.setDynamicAction = {
        update: (element, valueAccessor, allBindings, viewModel) => {
            let val = valueAccessor;
        },
        preprocess: (value, name, addBinding) => {
            let str,
                action = options.action;
            str = value === '1' ? '{mouseenter: $root.toggleInner, mouseleave: $root._collapseAll}' : '{mouseenter: $root.toggleInner}';
            if (action === 'hover') {
                addBinding('click', `function (){  
                    return true; 
                }`);
                addBinding('event', str);
            } else {
                addBinding('click', '$root.toggleInner');
                addBinding('event', '{}');
            }
        }
    }
}