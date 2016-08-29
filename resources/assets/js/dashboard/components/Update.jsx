import React, { PropTypes } from 'react';

import Icon from '../../app/components/Icon';

const Update = (props) => {
  const {
    outdated,
    latest,
    version,
  } = props;

  const strings = {
    title: Lang.get('app.update_available'),
    output: Lang.get('app.outdated', {
      current: version,
      latest,
      link: 'https://github.com/REBELinBLUE/deployer/releases/latest',
    }),
  };

  if (!outdated) {
    return null;
  }

  return (
    <div className="alert alert-info" id="update-available">
      <h4><Icon fa="cloud-download" className="icon" /> {strings.title}</h4>
      <strong dangerouslySetInnerHTML={{ __html: strings.output }} />
    </div>
  );
};

Update.propTypes = {
  outdated: PropTypes.bool.isRequired,
  latest: PropTypes.string.isRequired,
  version: PropTypes.string.isRequired,
};

export default Update;
