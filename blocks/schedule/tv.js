/**
 * Build the block
 */
(function( blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/tv-mode', {
        title: 'Accès TV',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Affiche les emplois du temps sur la TV";
        },
        save: function() {
            return "test";
        },
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
));
