/**
 * Build the block
 */
( function(blocks, element, data)
{
    var el = element.createElement;
    console.log('test');

    blocks.registerBlockType('tvconnecteeamu/tablet-schedule', {
        title: '(Tablette) Emploi du temps',
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
