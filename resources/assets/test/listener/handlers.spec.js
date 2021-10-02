import { expect, use } from 'chai';
import sinon from 'sinon';
import dirtyChai from 'dirty-chai';
import sinonChai from 'sinon-chai';

import handlers from '../../src/listener/handlers';
import * as events from '../../src/listener/events';

use(dirtyChai);
use(sinonChai);

describe('Listener', () => {
  let listenerSpy;
  let handler;

  const listener = {
    on: () => {},
  };

  beforeEach(() => {
    listenerSpy = sinon.spy(listener, 'on');
    handler = handlers(listener);
  });

  afterEach(() => {
    listenerSpy.restore();
  });

  const testListenerSpy = (expectedEvent) => {
    expect(listenerSpy).to.have.been.calledOnce();
    expect(listenerSpy.getCall(0).args[0]).to.be.equal(expectedEvent);
    expect(listenerSpy.getCall(0).args[1]).to.be.a('function');
  };

  it('Should handle generic events', () => {
    const expectedEvent = 'a-socket-io-emitted-event';

    handler.on(expectedEvent, () => {});

    testListenerSpy(expectedEvent);
  });

  it('Should handle model updated events', () => {
    handler.onUpdate('project', () => {});

    testListenerSpy(`project:${events.MODEL_CHANGED}`);
  });

  it('Should handle model created events', () => {
    handler.onCreate('project', () => {});

    testListenerSpy(`project:${events.MODEL_CREATED}`);
  });

  it('Should handle model trashed events', () => {
    handler.onTrash('project', () => {});

    testListenerSpy(`project:${events.MODEL_TRASHED}`);
  });
});
