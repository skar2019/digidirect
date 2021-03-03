import {loadView} from 'digidirectUtils';

/** Class representing Navigation Component */
export default class Component {
    /**
     * Create a component instatnce
     * @param options
     */
    constructor (options) {
        this.options = Object.assign({}, this.options, options);
        loadView(this.options, this, 'Digidirect_Navigation/js/dist/views/index', 'Navigation');
    }
}
