import $ from 'jquery';
import Backbone from 'backbone';
import ace from 'brace';
import 'brace/mode/sh';

import CommandsCollection from '../collections/Commands';
import ModelViewFactory from '../factories/ModelViewFactory';
import bindDialogs from '../handlers/dialogs';
import listener from '../listener';
import { isCurrentTarget } from '../utils/target';
import reorderModels from '../handlers/reorderModels';
import routes from '../routes';

const element = 'command';
const translationKey = 'commands';

const ModelView = ModelViewFactory(element, ['name', 'step', 'user']);

let editor;
$(`div#${element}.modal`)
  .on('show.bs.modal', (event) => {
    editor = ace.edit(`${element}_script`);
    editor.getSession().setMode('ace/mode/sh');

    const button = $(event.relatedTarget);
    if (!button.hasClass('btn-edit')) {
      $(`#${element}_step`).val(button.data('step'));
      $(`#${element}_default_on_row`).addClass('hide');
    }
  })
  .on('hidden.bs.modal', () => {
    editor.setValue('');
    editor.gotoLine(1);
    editor.destroy();
    $(`#${element}_script`).text('');
  });

$(`#${element}_optional`).on('change', (event) => {
  $(`#${element}_default_on_row`).addClass('hide');

  if ($(event.currentTarget).is(':checked') === true) {
    $(`#${element}_default_on_row`).removeClass('hide');
  }
});

class CommandView extends ModelView {
  editModel() {
    super.editModel();

    $(`#${element}_script`).text(this.model.get('script'));
    $(`#${element}_optional`).prop('checked', (this.model.get('optional') === true));
    $(`#${element}_default_on`).prop('checked', (this.model.get('default_on') === true));

    $(`#${element}_default_on_row`).addClass('hide');
    if (this.model.get('optional') === true) {
      $(`#${element}_default_on_row`).removeClass('hide');
    }

    $(`.${element}-server`).prop('checked', false);
    $(this.model.get('servers')).each((index, server) => {
      $(`#${element}_server_${server.id}`).prop('checked', true);
    });
  }
}

const getInput = () => {
  const serverIds = [];
  $(`.${element}-server:checked`).each((index, server) => {
    serverIds.push(parseInt($(server).val(), 10));
  });

  return {
    name: $(`#${element}_name`).val(),
    script: editor.getValue(),
    user: $(`#${element}_user`).val(),
    step: $(`#${element}_step`).val(),
    target_type: $('input[name="target_type"]').val(),
    target_id: parseInt($('input[name="target_id"]').val(), 10),
    servers: serverIds,
    optional: $(`#${element}_optional`).is(':checked'),
    default_on: $(`#${element}_default_on`).is(':checked'),
  };
};

bindDialogs(element, translationKey, getInput, CommandsCollection);

reorderModels(element, routes.commandsReorder);

export default class CommandsCollectionView extends Backbone.View {
  constructor(step, options) {
    super({
      ...options,
      el: '#app',
    });

    this.collection = CommandsCollection;
    this.step = parseInt(step, 10);

    this.$beforeList = $(`#${element}s-before .${element}-list tbody`);
    this.$afterList = $(`#${element}s-after .${element}-list tbody`);

    this.listeners();
    this.render();
  }

  listeners() {
    this.listenTo(this.collection, 'add', this.addOne);
    this.listenTo(this.collection, 'reset', this.addAll);
    this.listenTo(this.collection, 'remove', this.addAll);
    this.listenTo(this.collection, 'all', this.render);

    // FIXME: Duplicated
    listener.onUpdate(element, (data) => {
      const model = this.collection.get(parseInt(data.model.id, 10));

      if (model) {
        model.set(data.model);
      }
    });

    listener.onTrash(element, (data) => {
      const model = this.collection.get(parseInt(data.model.id, 10));

      if (model) {
        this.collection.remove(model);
      }
    });

    listener.onCreate(element, (data) => {
      if (isCurrentTarget(data.model)) {
        const step = parseInt(data.model.step, 10);

        if (step + 1 === this.step || step - 1 === this.step) {
          this.collection.add(data.model);
        }
      }
    });
  }

  render() {
    $(`.no-${element}s`).show();
    $(`.${element}-list`).hide();

    const before = this.collection.find(model => model.isBefore());

    if (typeof before !== 'undefined') {
      $(`#${element}s-before .no-${element}s`).hide();
      $(`#${element}s-before .${element}-list`).show();
    } else {
      $(`#${element}s-before .no-${element}s`).show();
      $(`#${element}s-before .${element}-list`).hide();
    }

    const after = this.collection.find(model => model.isAfter());

    if (typeof after !== 'undefined') {
      $(`#${element}s-after .no-${element}s`).hide();
      $(`#${element}s-after .${element}-list`).show();
    } else {
      $(`#${element}s-after .no-${element}s`).show();
      $(`#${element}s-after .${element}-list`).hide();
    }
  }

  addOne(model) {
    const view = new CommandView({
      model,
    });

    if (model.isAfter()) {
      this.$afterList.append(view.render().el);
    } else {
      this.$beforeList.append(view.render().el);
    }
  }

  addAll() {
    this.$beforeList.html('');
    this.$afterList.html('');

    this.collection.each(this.addOne, this);
  }
}
