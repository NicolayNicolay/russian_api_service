// Функция, определяющая величину объекта
Object.size = function (obj) {
  let size = 0, key;
  for (key in obj) {
    if (obj.hasOwnProperty(key)) {
      size++;
    }
  }
  return size;
};

Array.prototype.insert = function (index, item) {
  this.splice(index, 0, item);
};
