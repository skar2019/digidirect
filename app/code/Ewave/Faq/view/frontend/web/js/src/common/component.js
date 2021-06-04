import Model from './../models/index';
import {Store, Events} from './store';
import {loadView} from 'ewaveUtils';

export default class Component {
    constructor (options) {
        this.options = options;
        this.model = new Model(this.options);

        loadView(this.options, this, 'Ewave_Faq/js/dist/views/index', 'FAQ');

        this.watchers(this.options);
    }

    async fetchData (url, fetchOptions) {
        try {
            if (!url) {
                return;
            }

            // start progress
            Store.emit(Events.DATA_FETCH_PROGRESS);

            // model fetch data
            let data = await this.model.fetchData(url, fetchOptions);

            this.updateBrowserHistoryState(fetchOptions);

            Store.emit(Events.DATA_FETCH_SUCCESS, data);
        } catch (error) {
            console.warn('FAQ request failed', error);
            Store.emit(Events.ERROR, error);
        }
    }

    watchers (options) {
        Store.on(Events.DATA_FETCH_START, (url, realUrl) => {
            if (Store.currentState === Events.DATA_FETCH_PROGRESS) {
                return;
            }

            this.fetchData(url, {
                realUrl: realUrl
            });
        });
    }

    updateBrowserHistoryState (fetchOptions) {
        if (fetchOptions !== undefined && fetchOptions.realUrl) {
            let url = fetchOptions.realUrl;
            window.history.pushState({'url': url}, '', url);
        }
    }
}
