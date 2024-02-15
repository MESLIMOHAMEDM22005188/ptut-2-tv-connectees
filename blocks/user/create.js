/**
 * Build the block
 */
( function(blocks, element, data)
{
    var el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/creation-user', {
        title: 'Crée les utilisateurs demandés',
        icon: 'smiley',
        category: 'common',

        edit: function() {
            return "Crée les utilisateurs demandés";
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
