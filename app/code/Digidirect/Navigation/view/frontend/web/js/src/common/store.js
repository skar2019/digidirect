/**
 * @module Global Store
 */
import EventPubSub from 'Digidirect_Utilities/js/dist/vendor/event-pubsub';
import * as Constants from './constants';

export const Store = new EventPubSub;
export const Events = Constants;