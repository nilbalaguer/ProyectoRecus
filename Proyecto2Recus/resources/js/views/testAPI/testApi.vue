<script setup>
import { ref } from 'vue';
import axios from 'axios';

const responses = ref([]);
const requestMethod = ref('GET');
const requestUrl = ref('http://127.0.0.1:8000/api/');
const requestData = ref('');
const requestParams = ref([{ key: '', value: '' }]);

function addParam() {
    requestParams.value.push({ key: '', value: '' });
}

function removeParam(index) {
    requestParams.value.splice(index, 1);
}

async function sendRequest() {
    try {
        let res;
        const paramsObj = {};
        requestParams.value.forEach(param => {
            if (param.key.trim() !== '') {
                paramsObj[param.key] = param.value;
            }
        });

        if (requestMethod.value === 'GET') {
            res = await axios.get(requestUrl.value, { params: paramsObj });
        } else if (requestMethod.value === 'POST') {
            res = await axios.post(requestUrl.value, JSON.parse(requestData.value || '{}'));
        } else if (requestMethod.value === 'PUT') {
            res = await axios.put(requestUrl.value, JSON.parse(requestData.value || '{}'));
        } else if (requestMethod.value === 'DELETE') {
            res = await axios.delete(requestUrl.value);
        }

        responses.value.unshift({ method: requestMethod.value, url: requestUrl.value, data: res.data });
    } catch (error) {
        console.error("Error:", error);
        responses.value.unshift({
            method: requestMethod.value,
            url: requestUrl.value,
            data: error.response ? error.response.data : "Request failed"
        });
    }
}
</script>

<template>
    <div class="w-50 d-flex flex-column m-auto">
        <h1 class="text-center">API Request Testing</h1>

        <label>Request Method:</label>
        <select v-model="requestMethod">
            <option value="GET">GET</option>
            <option value="POST">POST</option>
            <option value="PUT">PUT</option>
            <option value="DELETE">DELETE</option>
        </select>

        <label>Set URL:</label>
        <input v-model="requestUrl" type="text" style="border: 1px solid white !important;" />

        <!-- Only show GET params input when using GET -->
        <div v-if="requestMethod === 'GET'" class="mt-2">
            <label>GET Params:</label>
            <div v-for="(param, index) in requestParams" :key="index" class="d-flex gap-2 mb-1">
                <input v-model="param.key" placeholder="Key" style="flex: 1; border: 1px solid white !important;" />
                <input v-model="param.value" placeholder="Value" style="flex: 1; border: 1px solid white !important;" />
                <button @click="removeParam(index)" type="button">X</button>
            </div>
            <button @click="addParam" type="button">+ Add Param</button>
        </div>

        <label v-if="requestMethod !== 'GET'">Request Data (JSON):</label>
        <textarea v-if="requestMethod !== 'GET'" v-model="requestData" style="border: 1px solid white !important;"></textarea>

        <button @click="sendRequest" class="mt-3">Send Request</button>

        <label>Request Data:</label>
        <div>
            <pre v-for="(res, index) in responses" :key="index" class="p-3" style="border: 1px dashed rgb(200, 200, 200); white-space: pre-wrap;">
<strong>{{ res.method }} - {{ res.url }}</strong>
{{ res.data }}
            </pre>
        </div>
        <router-link to="/admin">Admin page</router-link>
    </div>
</template>
