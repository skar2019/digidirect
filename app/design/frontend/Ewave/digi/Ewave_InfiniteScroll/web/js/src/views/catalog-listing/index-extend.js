import ViewCustom from './../catalog-listing/index';

export default class ViewAdditional extends ViewCustom {
    _setButtonContent (currentCount, totalCount) {
        return `Load more`;
    }
}