define([
    'uiComponent',
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            helpFlag: false,
            contentVisibility: true,
            additionalClass: false,
            isVisible: true,
            imports: {
                actionClickable: '${$.provider}:data.action_clickable',
                isCarVisibleToggle: '${$.provider}:data.car_icon_visible',
                closeable: '${$.provider}:data.closeable',
                textSize: '${$.provider}:data.text_size',
                fontFamily: '${$.provider}:data.text_font',
                barBackground: '${$.provider}:data.background_color',
                extraColor: '${$.provider}:data.extra_color',
                textColor: '${$.provider}:data.text_color',
                actionLink: '${$.provider}:data.action_link',
                goal: '${$.provider}:data.goal',
                goalLeft: '${$.provider}:data.goal_left',
                goalSource: '${$.provider}:data.goal_source',
                currencySymbol: '${$.provider}:data.currency_symbol',
                customStyle: '${$.provider}:data.custom_style',
            },
            modules: {
                initMessage: 'amasty_shipbar_profile_form.amasty_shipbar_profile_form.content.init_message',
                progressMessage: 'amasty_shipbar_profile_form.amasty_shipbar_profile_form.content.progress_message',
                achievedMessage: 'amasty_shipbar_profile_form.amasty_shipbar_profile_form.content.achieved_message',
                termsMessage: 'amasty_shipbar_profile_form.amasty_shipbar_profile_form.content.terms_message'
            }
        },

        initObservable: function () {
            this._super().observe([
                'helpFlag',
                'contentVisibility',
                'actionClickable',
                'isCarVisibleToggle',
                'closeable',
                'textSize',
                'fontFamily',
                'barBackground',
                'extraColor',
                'textColor',
                'actionLink',
                'goal',
                'goalLeft',
                'goalSource',
                'customStyle'
            ]);

            return this;
        },

        isCarVisible: function () {
            return this.isCarVisibleToggle() === "1" || !this.contentVisibility();
        },

        hasTermContent: function () {
            var result = this.getMessageByFieldset(this.termsMessage()).length > 0;

            if (!result) {
                this.helpFlag(result);
            }

            return result;
        },

        initMoving: function () {
        },

        haveAction: function () {
            return this.actionClickable() === "1" && this.actionLink().length > 0;
        },

        isCloseable: function () {
            return this.closeable() === "1";
        },

        haveCustomStyle: function () {
            return this.customStyle().length > 0;
        },

        getLabelContent: function () {
            var text = this.getRawLabelContent();

            if (this.goalSource() == 0) {
                text = text.replace('{{ruleGoal}}', this.addExtraColor(Math.round(this.goal() * 100) / 100));
                text = text.replace('{{ruleGoalLeft}}', this.addExtraColor(Math.round(this.goal() * this.goalLeft()) / 100));
            } else {
                text = text.replace('{{ruleGoal}}', this.addExtraColor('{Free Shipping Amount}'));
                text = text.replace('{{ruleGoalLeft}}', this.addExtraColor('{Free Shipping Amount}'));
            }

            return text;
        },

        getFontSize: function () {
            return this.textSize() + 'px';
        },

        getMessageByFieldset: function (element) {
            return element._elems[10]._elems[10]._elems[10].value();
        },

        getRawLabelContent: function () {
            if (this.helpFlag()) {
                return this.getMessageByFieldset(this.termsMessage());
            }

            switch (this.goalLeft()) {
                case "100":
                    return this.getMessageByFieldset(this.initMessage());
                case "50":
                    return this.getMessageByFieldset(this.progressMessage());
                case "0":
                    return this.getMessageByFieldset(this.achievedMessage());
            }

            return this.getMessageByFieldset(this.initMessage());
        },

        addExtraColor: function (data) {
            return "<b style=\"color: " + this.extraColor() + "\">" + this.currencySymbol + data  + "</b>";
        },
    });
});
