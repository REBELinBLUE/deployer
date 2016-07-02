import React from 'react';

const Update = (props) => {
  const strings = {
    title: Lang.get('app.update_available'),
    output: Lang.get('app.outdated'),
  };

  //Lang::get('app.outdated', ['current' => $current_version, 'latest' => $latest_version, 'link' => 'https://github.com/REBELinBLUE/deployer/releases/latest' ])

  return (
    <div className="alert alert-info" id="update-available">
      <h4><i className="icon fa fa-cloud-download"></i> {strings.title}</h4>
      <strong>{strings.output}</strong>
    </div>
  );
};

export default Update;
