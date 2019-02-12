import EventPubSubExtended from 'eventPubSubExtended';
import * as Constants from './constants';

export default class StoreClass {
    constructor () {
        this.Store = new EventPubSubExtended(Constants);
        this.Events = Constants;
    }
}
