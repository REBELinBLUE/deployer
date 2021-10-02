import * as events from './events';

export default listener => ({
  on: (channel, callback) => {
    listener.on(channel, callback);
  },

  onUpdate: (model, callback) => {
    listener.on(`${model}:${events.MODEL_CHANGED}`, callback);
  },

  onCreate: (model, callback) => {
    listener.on(`${model}:${events.MODEL_CREATED}`, callback);
  },

  onTrash: (model, callback) => {
    listener.on(`${model}:${events.MODEL_TRASHED}`, callback);
  },
});
