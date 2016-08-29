### Create release archive - {{ deployment }}
cd {{ mirror_path }}

(git archive --format=tar {{ sha }} | gzip > {{ release_archive }})
