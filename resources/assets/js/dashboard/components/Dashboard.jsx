import React from 'react';

import GroupedProjectList from '../containers/Projects';
import Timeline from '../containers/Timeline';
import UpdateAlert from '../containers/Update';

const Dashboard = () => (
  <div>
    <UpdateAlert />

    <div className="row">
      <div className="col-md-7">
        <GroupedProjectList />
      </div>

      <div className="col-md-5">
        <Timeline />
      </div>
    </div>
  </div>
);

export default Dashboard;
