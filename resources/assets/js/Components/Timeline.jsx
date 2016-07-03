import React from 'react';

const Timeline = () => {
  const strings = {
    timeline: Lang.choice('dashboard.latest', 2),
  };

  return (
    <div className="box">
      <div className="box-header">
        <h3 className="box-title">{strings.timeline}</h3>
      </div>
      <div className="box-body" id="timeline">
        timeline
      </div>
    </div>
  );
};

export default Timeline;
