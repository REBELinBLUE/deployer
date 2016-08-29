import { PropTypes } from 'react';

// Reusable react shape for Project model
export default PropTypes.shape({
  id: PropTypes.number.isRequired,
  allow_other_branch: PropTypes.bool.isRequired,
  branch: PropTypes.string.isRequired,
  branch_url: PropTypes.string.isRequired,
  builds_to_keep: PropTypes.number.isRequired,
  build_url: PropTypes.string.isRequired,
  deployments_today: PropTypes.number.isRequired,
  group_id: PropTypes.number.isRequired,
  include_dev: PropTypes.bool.isRequired,
  latest_deployment_runtime: PropTypes.oneOfType([
    PropTypes.number,
    PropTypes.bool,
  ]).isRequired,
  last_run: PropTypes.string,
  name: PropTypes.string.isRequired,
  public_key: PropTypes.string.isRequired,
  recent_deployments: PropTypes.number.isRequired,
  repository: PropTypes.string.isRequired,
  repository_path: PropTypes.string.isRequired,
  repository_url: PropTypes.string.isRequired,
  status: PropTypes.number.isRequired,
  url: PropTypes.string.isRequired,
  webhook_url: PropTypes.string.isRequired,
});
