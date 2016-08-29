import React from 'react';
import { createDevTools } from 'redux-devtools';
import LogMonitor from 'redux-devtools-log-monitor';
import DockMonitor from 'redux-devtools-dock-monitor';
import SliderMonitor from 'redux-slider-monitor';
import ChartMonitor from 'redux-devtools-chart-monitor';
import Dispatcher from 'redux-devtools-dispatch';
import MultipleMonitors from 'redux-devtools-multiple-monitors';
import Inspector from 'redux-devtools-inspector';

import * as AppActions from '../../../actions';

const tooltipOptions = {
  style: {
    'background-color': '#ffffff',
    opacity: '0.9',
    'border-radius': '5px',
    padding: '5px',
  },
};

const ReduxDevTools = createDevTools(
  <DockMonitor
    toggleVisibilityKey="ctrl-h"
    changePositionKey="ctrl-p"
    changeMonitorKey="ctrl-m"
    defaultSize={0.2}
    defaultPosition="right"
    defaultIsVisible={false}
  >
    <MultipleMonitors>
      <LogMonitor theme="tomorrow" preserveScrollTop={false} />
      <Dispatcher theme="tomorrow" actionCreators={AppActions} />
    </MultipleMonitors>
    <SliderMonitor theme="tomorrow" />
    <ChartMonitor theme="tomorrow" tooltipOptions={tooltipOptions} invertTheme />
    <Inspector theme="tomorrow" isLightTheme={false} supportImmutable />
  </DockMonitor>
);

const DevTools = () => (!window.devToolsExtension ? <ReduxDevTools /> : null);

export default DevTools;
export { ReduxDevTools };
