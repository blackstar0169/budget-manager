/**
 * Fast and insecure string hashing function
 * @url https://gist.github.com/hyamamoto/fd435505d29ebfa3d9716fd2be8d42f0
 * @param {string} s String to hash
 * @returns {number} Hash value
 */
export function hash(s) {
    var h = 0, l = s.length, i = 0;
    if ( l > 0 ) {
        while (i < l)
        h = (h << 5) - h + s.charCodeAt(i++) | 0;
    }
    return h;
}


/**
 * Extract
 * @url https://gist.github.com/hyamamoto/fd435505d29ebfa3d9716fd2be8d42f0
 * @param {string} s String to hash
 * @returns {number} Hash value
 */
export function only(obj, keys, withKeys) {
    if (typeof withKeys !== 'boolean') {
        withKeys = true;
    }

    var ret = withKeys ? {} : [];

    for (const key in obj) {
        if (Object.hasOwnProperty.call(obj, key) && keys.indexOf(key) >= 0) {
            if (withKeys) {
                ret[key] = obj[key];
            } else {
                ret.push(obj[key]);
            }
        }
    }

    return ret;
}

/**
 * Fast and insecure string hashing function
 * @url https://gist.github.com/hyamamoto/fd435505d29ebfa3d9716fd2be8d42f0
 * @param {string} s String to hash
 * @returns {number} Hash value
 */
export function except(obj, keys, withKeys) {
    if (typeof withKeys !== 'boolean') {
        withKeys = true;
    }

    var ret = withKeys ? {} : [];

    for (const key in obj) {
        if (Object.hasOwnProperty.call(obj, key) && keys.indexOf(key) === 0) {
            if (withKeys) {
                ret[key] = obj[key];
            } else {
                ret.push(obj[key]);
            }
        }
    }

    return ret;
}

export function round(number, decimalPlace) {
    const factorOfTen = Math.pow(10, decimalPlace)
    return Math.round((number + Number.EPSILON) * factorOfTen) / factorOfTen
}
