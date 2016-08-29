import { connect } from 'react-redux';

import * as constants from '../../constants';
import DeploymentTab from '../../components/deployments/DeploymentTab';

const mapStateToProps = (state) => {
  const deployments = state.getIn([constants.NAME, 'deployments']).toJS();

  return {
    ...deployments,
    fetching: state.getIn([constants.NAME, 'fetching']),
  };
};

export default connect(mapStateToProps)(DeploymentTab);
//
// "deployments": {
//   "total": 0,
//     "per_page": 15,
//     "current_page": 1,
//     "last_page": 0,
//     "next_page_url": null,
//     "prev_page_url": null,
//     "from": null,
//     "to": null,
//     "data": []
// },
