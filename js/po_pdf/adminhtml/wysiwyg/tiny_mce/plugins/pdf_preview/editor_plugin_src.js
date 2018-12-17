/**(function() {
    tinymce.create('tinymce.plugins.PdfPreview', {
        init : function(ed, url) {
            // Register Widget plugin button
            window.PdfPreview = function()
            {
                if (!$('edit_form')) {
                    return;
                }
                var oldActionUrl = $('edit_form').action;
                $('edit_form').action = ed.settings.magentoPluginsOptions.get('pdfpreview').plugin_preview_pageurl;
                $('edit_form').submit();
                $('edit_form').action = oldActionUrl;
            };
            ed.addCommand('mcePdfPreview', function() {
                PdfPreview()
            });
            ed.addButton('pdfpreview', {
                title : ed.settings.magentoPluginsOptions.get('pdfpreview').title,
                image : url + '/img/icon.gif',
                cmd : 'mcePdfPreview'
            });
        },
        getInfo : function() {
            return {
                longname : 'Magento PDF preview Plugin for TinyMCE 3.x',
                author : 'Potato Team',
                authorurl : 'http://potatocommerce.com',
                infourl : 'http://potatocommerce.com',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('pdfpreview', tinymce.plugins.PdfPreview);
})();**/
window.PdfPreview = function(url)
{
    if (!$('edit_form')) {
        return;
    }
    var oldActionUrl = $('edit_form').action;
    $('edit_form').action = url;
    $('edit_form').submit();
    $('edit_form').action = oldActionUrl;
};