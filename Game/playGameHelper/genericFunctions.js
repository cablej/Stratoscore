//Generic function to remove an element from an array
Array.prototype.remove = function(from, to) {
    var rest = this.slice((to || from) + 1 || this.length);
    this.length = from < 0 ? this.length + from : from;
    return this.push.apply(this, rest);
};

//Checks to see if a string is in an array
function contains(array, string) {
    for (i = 0; i < array.length; i++) {
        if (array[i] == string) {
            return true;
        }
    }
    return false;
}

Array.prototype.removeAt = function(e) {
    var t, _ref;
    if ((t = this.indexOf(e)) > -1) {
        return ([].splice.apply(this, [t, t - t + 1].concat(_ref = [])), _ref);
    }
};