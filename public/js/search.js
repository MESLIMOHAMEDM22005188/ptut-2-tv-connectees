/**
 * Searches an element in the table
 *
 * @param idTable
 */
function search(idTable)
{
    const table = document.getElementById('table' + idTable);
    const input = document.getElementById('key' + idTable);
    
    const value = input.value.toLowerCase();
    
    const rows = table.children[1].getElementsByTagName('tr');
        
    for (let i = rows.length; i--;) {
      let columns = rows[i].getElementsByTagName('td');
      rows[i].style.removeProperty('display');
      
      if (value === '') continue;
      let hide = 0;
      
      for (let j = columns.length; j--;) {
        let td = columns[j];
        let text = td.innerText.toLowerCase();
                
        if (isNaN(text) && isNaN(value) && !text.includes(value))
          continue;
        else if (!isNaN(text) && !isNaN(value) && text !== value)
          continue;
        else if (!text.toLowerCase().includes(value))
          continue;
        else hide += 1;
      }
      
      if (hide == 0) rows[i].style.display = 'none';
    }
    
}