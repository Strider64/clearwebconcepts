class FetchRequest {
    constructor(url) {
        this.url = url;
    }

    async createRequest(method, data, succeed, fail) {
        try {
            const response = await fetch(this.url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            this.handleErrors(response);

            const responseData = await response.json();
            succeed(responseData);
        } catch (error) {
            fail(error);
        }
    }

    handleErrors(response) {
        if (!response.ok) {
            throw Error(`HTTP error! status: ${response.status}`);
        }
        if (!response.headers.get('content-type').includes('application/json')) {
            throw new Error('Not a JSON response');
        }
    }

}