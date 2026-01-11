export function getChangedPositions(listItems = [], orderedIds = []) {
  let changes = [];
  listItems.forEach((item) => {
    orderedIds.forEach((id, index) => {
      if (item.id === id && parseInt(item.position) !== index) {
        changes.push({
          id: id,
          position: index
        });
      }
    });
  });

  return changes;
}

export function getChangedElements(listItems = [], orderedItems = [], keysToCompare = []) {
  let changes = [];
  listItems.forEach((item) => {
    orderedItems.forEach((changedItem, index) => {
      if (item.id === changedItem.id && isChanged(item, changedItem, keysToCompare)) {
        let element = {
          id: changedItem.id
        };
        keysToCompare.forEach((key) => {
          if (changedItem.hasOwnProperty(key)) {
            element[key] = changedItem[key];
          } else if(item.hasOwnProperty(key)) {
            element[key] = item[key];
          }
        })
        changes.push(element);
      }
    });
  });

  return changes;
}

function isChanged(item = {}, changedItem = {}, keysToCompare = []) {
  let isEqual = true;
  keysToCompare.forEach((key) => {
    if (item[key] !== changedItem[key]) {
      isEqual = false;
    }
  })

  return !isEqual;
}


