import React from 'react';

import GroupedProjectList from '../Containers/Projects';
import Timeline from './Timeline';
import UpdateAlert from '../Containers/Update';

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
