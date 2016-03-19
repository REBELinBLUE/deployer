chmod +x "{{ wrapper_file }}" && \
export GIT_SSH="{{ wrapper_file }}" && \
( [ ! -d {{ mirror_path }} ] && git clone --mirror {{ repository }} {{ mirror_path }} || cd {{ mirror_path }} ) && \
cd {{ mirror_path }} && \
git fetch --all --prune
