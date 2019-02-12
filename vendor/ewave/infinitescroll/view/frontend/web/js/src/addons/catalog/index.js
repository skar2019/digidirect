import {GlobalStore, GlobalEvents} from 'ewaveStoreCatalog';
import {Store, Events} from './../../common/store';

export default class AddOn {
    constructor (options) {
        GlobalStore.on(GlobalEvents.PRODUCT_COLLECTION_UPDATED, (data) => {
            if (data.page_params.nextUrl) {
                data.page_params.nextUrl = data.page_params.nextUrl + '&_is=' + options.perPageCount;
            }
            this.options = Object.assign({}, options, data.page_params);
            Store.emit(Events.RELOAD, this.options);
        });
    }
}
