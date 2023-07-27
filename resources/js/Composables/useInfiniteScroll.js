export default (data) => {
    const loadMore = (queryParams) => {
        if (!data.value.links.next) return Promise.resolve();

        return axios
            .get(data.value.links.next, {
                params: queryParams,
            })
            .then((response) => {
                data.value = {
                    ...response.data,
                    data: [...data.value.data, ...response.data.data],
                };
            });
    };

    return {
        loadMore,
    };
};
