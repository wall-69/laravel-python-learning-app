export function getTimeFormatted() {
    return (
        "[" +
        new Date().toLocaleDateString("sk-SK") +
        " " +
        new Date().toLocaleTimeString("sk-SK") +
        "]"
    );
}

export function log(message) {
    console.log(getTimeFormatted() + " " + message);
}

export function warn(message) {
    console.warn(getTimeFormatted() + " " + message);
}

export function error(message) {
    console.error(getTimeFormatted() + " " + message);
}
