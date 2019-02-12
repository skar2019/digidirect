import {loadView} from 'ewaveUtils';
import StoreClass from './store';

export default class Component {
    constructor (options, element) {
        this.options = options;
        this.element = element;
        this.bind();
    }

    bind () {
        let options = this.options,
            element = this.element;
        const Store = new StoreClass();

        loadView({options, element, ...Store}, this, 'Ewave_RelatedProduct/js/dist/views/index', 'Related Product');
    }
}
