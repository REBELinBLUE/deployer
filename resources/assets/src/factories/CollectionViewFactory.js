import Backbone from 'backbone';
import $ from 'jquery';

import listener from '../listener';
import { isCurrentTarget, isCurrentProject } from '../utils/target';

export default (element, Collection, ModelView) =>
  class CollectionView extends Backbone.View {
    constructor(options) {
      super({
        ...options,
        el: `#${element}_list tbody`,
      });

      this.collection = Collection;

      this.listeners();
      this.render();
    }

    render() {
      if (this.collection.length > 0) {
        $(`#no_${element}s`).hide();
        $(`#${element}_list`).show();
      } else {
        $(`#no_${element}s`).show();
        $(`#${element}_list`).hide();
      }
    }

    listeners() {
      this.listenTo(this.collection, 'add', this.addOne);
      this.listenTo(this.collection, 'reset', this.addAll);
      this.listenTo(this.collection, 'remove', this.addAll);
      this.listenTo(this.collection, 'all', this.render);

      listener.onUpdate(element, this.modelChanged());
      listener.onTrash(element, this.modelTrashed());
      listener.onCreate(element, this.modelCreated());
    }

    modelChanged() {
      const self = this;

      return (data) => {
        const model = self.collection.get(parseInt(data.model.id, 10));

        if (model) {
          model.set(data.model);
        }
      };
    }

    modelTrashed() {
      const self = this;

      return (data) => {
        const model = self.collection.get(parseInt(data.model.id, 10));

        if (model) {
          model.set(data.model);
        }
      };
    }

    modelCreated() {
      const self = this;

      return (data) => {
        if (data.model.target_id && isCurrentTarget(data.model)) {
          self.collection.add(data.model);
        }

        if (data.model.project_id && isCurrentProject(data.model)) {
          self.collection.add(data.model);
        }
      };
    }

    addOne(model) {
      const view = new ModelView({
        model,
      });

      this.$el.append(view.render().el);
    }

    addAll() {
      this.$el.html('');

      this.collection.each(this.addOne, this);
    }
  };
