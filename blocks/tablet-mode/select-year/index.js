/**
 * Build the block
 */
( function(blocks, element, data)
{
    const el = element.createElement;

    blocks.registerBlockType('tvconnecteeamu/tablet-select-year', {
        title: '(Tablette) Sélectionner une année',
        icon: 'smiley',
        category: 'common',

        edit: function() {
          return 'test';
        },
        save: function() {
          return 'test';
        },
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
));
