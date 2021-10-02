import moment from 'moment';

export function logFormatter(log) {
  return log
    .replace(/<\/error>/g, '</span>')
    .replace(/<\/info>/g, '</span>')
    .replace(/<error>/g, '<span class="text-red">')
    .replace(/<info>/g, '<span class="text-default">');
}

export function dateTimeFormatter(datetime) {
  return moment(datetime).format('Do MMMM YYYY h:mm:ss A');
}

export function timeFormatter(datetime) {
  return moment(datetime).format('h:mm:ss A');
}
