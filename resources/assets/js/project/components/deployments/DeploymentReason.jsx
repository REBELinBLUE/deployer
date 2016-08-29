import React, { PropTypes } from 'react';

import Icon from '../../../app/components/Icon';

const DeploymentReason = (props) => {
  const {
    isWebhook,
    reason,
  } = props;

  const strings = {
    webhook: Lang.get('deployments.webhook'),
    manually: Lang.get('deployments.manually'),
  };

  return (
    <span>
      {isWebhook ? strings.webhook : strings.manually}
      {
        reason ?
          <Icon fa="comment-o" className="deploy-reason" data-toggle="tooltip" data-placement="right" title={reason} />
        :
          null
      }
    </span>
  );
};

DeploymentReason.propTypes = {
  isWebhook: PropTypes.bool.isRequired,
  reason: PropTypes.string,
};

export default DeploymentReason;
