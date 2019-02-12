import {loadView} from 'ewaveUtils';

export default class Component {
    constructor (options) {
        this.options = options;
        this.bind(this.options);
    }

    bind (options) {
        loadView(options, this, 'Ewave_MyStoreWidget/js/dist/view/index', 'MyStoreWidget');
    }
}
