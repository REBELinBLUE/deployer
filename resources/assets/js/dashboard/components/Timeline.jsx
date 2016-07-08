import React from 'react';

import Box from '../../app/components/Box';

const Timeline = () => {
  const strings = {
    timeline: Lang.choice('dashboard.latest', 2),
  };

  return (
    <Box title={strings.timeline} id="timeline">
      timeline
    </Box>
  );
};

export default Timeline;
