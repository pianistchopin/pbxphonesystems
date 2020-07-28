define([
  'ko',
  'jquery',
  'mageUtils',
  'Magento_Ui/js/grid/controls/columns',
  'uiLayout',
  'uiRegistry'
], function (ko, $, utils, Columns, layout, registry) {


  var r = Columns.extend({
    defaults: {
      selectedTab: 'tab1',
      template: 'Amasty_Pgrid/ui/grid/controls/columns',
      clientConfig: {
          component: 'Magento_Ui/js/grid/editing/client',
          name: '${ $.name }_client'
      },
      modules: {
          client: '${ $.clientConfig.name }',
          source: '${ $.provider }',
          editorCell: '${ $.editorCellConfig.provider }',
          listingFilter: '${ $.listingFilterConfig.provider }'
      }
    },
    initElement: function (el) {
      el.track(['label', 'ampgrid_editable', 'ampgrid_filterable', 'ampgrid_title'])
      el.headerTmpl = "Amasty_Pgrid/ui/grid/columns/text";
    },
    hasSelected: function(tabKey)  {
      return this.selectedTab == tabKey;
    },
    getDefaultColumns: function() {
      var c = [];
      this.elems.each(function(el) {
        if (el.ampgrid && !el.amastyExtra && !el.amastyAttribute) {
          c.push(el);
        }
      });
      return c;
    },
    getAtttributeColumns: function () {
      var c = [];
      this.elems.each(function (el) {
        if (el.ampgrid && el.amastyAttribute) {
          c.push(el);
        }
      });
      return c;
    },
    getExtraColumns: function () {
      var c = [];
      this.elems.each(function (el) {
        if (el.ampgrid && el.amastyExtra) {
          c.push(el);
        }
      });
      return c;
    },
    close: function () {
        return this;
    },
    onSaveError: function() {

    },
    initialize: function () {

      _.bindAll(this, 'onSaveError', 'reloadGridData');

      this._super();

      layout([this.clientConfig]);

      return this;
    },
    initObservable: function () {
      this._super()
          .track(['selectedTab']);

      return this;
    },
    prepareColumns: function () {
        var columns = this;
        columns.editorCell().model.columns('showLoader');
        this.elems.each(function (el) {
            var current = columns.storage().get("current.columns." + el.index);
            if (current) {
                current.visible = el.visible;
                current.ampgrid_title = el.ampgrid.title;
                current.ampgrid_editable = el.ampgrid.editable;
                current.ampgrid_filterable = el.ampgrid.filterable;
            }
            columns.editorCell().initColumn(el.index);

            var filter = columns.listingFilter().elems.findWhere({
                index: el.index
            });

            if (!filter && el.ampgrid.filterable) {
                el.filter = el.default_filter;
                columns.listingFilter().addFilter(el);
            }

            if (filter && !el.ampgrid.filterable) {
                filter.visible(false);
            } else if (filter && el.visible && el.ampgrid.filterable) {
                filter.visible(true);
            }
        });
    },

    reloadGridData: function(data) {
        if (data.visible === false) {
            return this;
        }
        this.prepareColumns();

        var currentData = this.source().get('params');
        currentData.data = JSON.stringify({'column': data.index});
        this.client()
            .save(currentData)
            .done(this.amastyReload)
            .fail(this.onSaveError);

        return this;
    },

    saveBookmark: function () {
        this.prepareColumns();
        this.storage().saveState();
        this.editorCell().model.columns('hideLoader');
    },

    amastyReload: function () {
        registry.get('index = product_listing').source.reload();
    }
  });

  return r;
});