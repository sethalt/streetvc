!function ($) {
    var ChoiceOrOther = function (element, options) {
        this.options = options
        this.$element = $(element)
        this.expanded = this.$element.data('expanded')
        this.$choice = this.expanded ? this.$element.find('[type=radio]').last() : this.$element.find('select');
        this.$other = this.$element.find('[data-role="other"]')
        this.$otherInput = this.$other.find('input')
        this.$element.delegate('[data-role="choice"]', 'change', $.proxy(this.check, this))
        this.check()
    }

    ChoiceOrOther.prototype = {

        constructor: ChoiceOrOther
        , check: function (e) {
            e && e.preventDefault()

            var other = this.expanded ? this.$choice.is(':checked') : !this.$choice.val() 
            if (!other) { 
                this.$otherInput.val('')
                this.$otherInput.prop('required', false);
                this.$other.hide()
            } else {
                this.$other.show()
                this.$otherInput.prop('required', true);
            }
        }
    }


    /* ChoiceOrOther PLUGIN DEFINITION
     * ======================= */

    var old = $.fn.choiceOrOther

    $.fn.choiceOrOther = function (option) {
      return this.each(function () {
        var $this = $(this)
          , data = $this.data('choiceOrOther')
          , options = $.extend({}, $.fn.choiceOrOther.defaults, $this.data(), typeof option == 'object' && option)
        if (!data) $this.data('choiceOrOther', (data = new ChoiceOrOther(this, options)))
      })
    }

    $.fn.choiceOrOther.defaults = {
    }

    $.fn.choiceOrOther.Constructor = ChoiceOrOther


    /* ChoiceOrOther NO CONFLICT
     * ================= */

    $.fn.choiceOrOther.noConflict = function () {
      $.fn.choiceOrOther = old
      return this
    }


    /* MODAL DATA-API
     * ============== */

    $(document).ready(function () {
        $('[data-toggle="choiceOrOther"]').each(function () {
            $(this).choiceOrOther()
        })
    }); 

}(window.jQuery);