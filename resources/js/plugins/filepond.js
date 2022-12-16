import {FilePond} from "filepond";
import { getToken } from "../lib/auth";

export default new FilePond.setOptions({
    server: {
        url: '/api/upload',
        process: {
            method: 'POST',
            withCredentials: true,
            headers: getToken()
        }
    }
})
