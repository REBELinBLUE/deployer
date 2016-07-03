import React from 'react';

import GroupedProjectList from './Projects';
import Timeline from '../Components/Timeline';
import UpdateAlert from '../Components/Update';

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
