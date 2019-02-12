import $ from 'jquery';
import View from '../../views/index';
import mageTemplate from 'mage/template';
import backTmpl from 'text!Ewave_Navigation/template/back.html';

export default class ViewCustom extends View {
    loadExtraLogic () {
        super.loadExtraLogic();

        if (this.options.responsive) {
            let links = $(`${this.options.wrapperClass} > .-parent > ${this.options.itemLabelClass}`),
                name, tmpl;

            links.each((index, element) => {
                name = $(element).text();

                tmpl = mageTemplate(backTmpl, {
                    name: name
                });

                $(tmpl).prependTo($(element).parent(this.options.itemClass).find(this.options.subMenuBlockClass).first());
            });

            links.on('click', () => {
                $('.menu-section').addClass('-sub-slide');
            });

            $(`${this.options.area} ${this.options.subMenuBlockClass} > .menu-back`).on('click', (e) => {
                let $this = $(e.currentTarget);

                $this.closest(this.options.subMenuBlockClass).removeClass('-open');
                $this.closest(this.options.itemClass).removeClass('-open');

                $('.menu-section').removeClass('-sub-slide');
            });
        }
    }
}
