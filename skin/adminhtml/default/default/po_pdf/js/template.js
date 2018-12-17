var PdfDefaultTemplateClass = Class.create();
PdfDefaultTemplateClass.prototype = {
    initialize: function(url, form) {
        this.url = url;
        this.form = form;
    },
    getRequest: function()
    {
        this.showMask();
        new Ajax.Request(this.url, {
            parameters: this.form ? this.form.serialize(true) : '',
            method: 'post',
            onSuccess: function(response) {
                this.hideMask();
                var json = JSON.parse(response.responseText);
                tinyMCE.get('content').setContent(json.content);
                return;
            }.bind(this)
        });
    },
    showMask: function()
    {
        if (!$('loading-mask').visible()) {
            $('loading-mask').show();
        }
        return;
    },
    hideMask: function()
    {
        if ($('loading-mask').visible()) {
            $('loading-mask').hide();
        }
        return;
    }
};