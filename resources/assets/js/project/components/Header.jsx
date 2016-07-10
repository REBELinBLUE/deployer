import React, { PropTypes } from 'react';

// fixme: convert to use "box", maybe turn the <li> content into components as they are the same pattern
const Header = (props) => {
  const { project } = props;

  const strings = {
    details: Lang.get('projects.details'),
    repository: Lang.get('projects.repository'),
    branch: Lang.get('projects.branch'),
    url: Lang.get('projects.url'),
    deployments: Lang.get('projects.deployments'),
    today: Lang.get('projects.today'),
    last_week: Lang.get('projects.last_week'),
    latest_duration: Lang.get('projects.latest_duration'),
    health: Lang.get('projects.health'),
    build_status: Lang.get('projects.build_status'),
    app_status: Lang.get('projects.app_status'),
    heartbeats_status: Lang.get('projects.heartbeats_status'),
  };

  return (
    <div className="row">
      <div className="col-md-4">
        <div className="box box-default">
          <div className="box-header with-border">
            <h3 className="box-title">{strings.details}</h3>
          </div>
          <div className="box-body no-padding">
            <ul className="nav nav-pills nav-stacked">
              <li>
                <a href={project.repository_url} target="_blank">
                  {strings.repository}
                  <span className="pull-right" title={strings.repository}>
                    <i className="fa {{ $project->type_icon }}"></i>
                    {project.repository_path}
                  </span>
                </a>
              </li>
              <li>
                <a href={project.branch_url} target="_blank">
                  {strings.branch}
                  <span className="pull-right label label-default">{project.branch}</span>
                </a>
              </li>
              {
                project.url ?
                  <li>
                    <a href={project.url} target="_blank">
                      {strings.url}
                      <span className="pull-right text-blue">{project.url}</span>
                    </a>
                  </li>
                :
                  null
              }
            </ul>
          </div>
        </div>
      </div>

      <div className="col-md-4">
        <div className="box box-default">
          <div className="box-header with-border">
            <h3 className="box-title">{strings.deployments}</h3>
          </div>
          <div className="box-body no-padding">
            <ul className="nav nav-pills nav-stacked">
              <li>
                <a href="#">
                  {strings.today}
                  <span className="pull-right">number_format($today)</span>
                </a>
              </li>
              <li>
                <a href="#">
                  {strings.last_week}
                  <span className="pull-right">number_format($last_week)</span>
                </a>
              </li>
              <li>
                <a href="#">
                  {strings.duration}
                  <span className="pull-right">(count($deployments) == 0 OR !$deployments[0]->finished_at) ? Lang::get('app.not_applicable') : $deployments[0]->readable_runtime</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div className="col-md-4">
        <div className="box box-default">
          <div className="box-header with-border">
            <h3 className="box-title">{strings.health}</h3>
          </div>
          <div className="box-body no-padding">
            <ul className="nav nav-pills nav-stacked">
              {
                project.build_url ?
                  <li>
                    <a href="#">
                      {strings.build_status}
                      <span className="pull-right"><img src={project.build_url} alt="" /></span>
                    </a>
                  </li>
                  :
                  null
              }
              <li>
                <a href="#">
                  {strings.app_status}
                  <span className="pull-right label label-{{ $project->app_status_css }}">
                    project->app_status
                  </span>
                </a>
              </li>
              <li>
                <a href="#">
                  {strings.heartbeats_status}
                  <span className="pull-right label label-{{ $project->heart_beat_status_css }}">
                    project->heart_beat_status
                  </span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
};

Header.propTypes = {
  project: PropTypes.object.isRequired,
};

export default Header;
