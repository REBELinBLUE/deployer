import React, { PropTypes } from 'react';

import Icon from '../../app/components/Icon';

const RepositoryIcon = (props) => {
  const { repository } = props;

  let fa = 'git-square';

  if (/github\.com/.test(repository)) {
    fa = 'github';
  } else if (/gitlab\.com/.test(repository)) {
    fa = 'gitlab';
  } else if (/bitbucket/.test(repository)) {
    fa = 'bitbucket';
  } else if (/amazonaws\.com/.test(repository)) {
    fa = 'amazon';
  }

  return (<Icon fa={fa} />);
};

RepositoryIcon.propTypes = {
  repository: PropTypes.string.isRequired,
};

export default RepositoryIcon;
