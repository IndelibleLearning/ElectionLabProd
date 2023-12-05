// roomsSlice.js
import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import { GET_ROOMS_URL, CREATE_ROOM_URL, getUserName, getLoginToken } from 'common/user_common.js';
import { post } from 'common/request.js';

// Async thunk for fetching rooms
export const fetchRooms = createAsyncThunk(
    'rooms/fetchRooms',
    async () => {
        const data = {
            "user_name": getUserName(),
            "token": getLoginToken()
        };
        const response = await post(GET_ROOMS_URL, data);
        return response;
    }
);

export const createRoom = createAsyncThunk(
    'rooms/createRoom',
    async (roomName, { dispatch, rejectWithValue }) => {
        try {
            const data = {
                "user_name": getUserName(),
                "token": getLoginToken(),
                "room_name": roomName
            };
            await post(CREATE_ROOM_URL, data);
            dispatch(fetchRooms());
        } catch (error) {
            return rejectWithValue(error.message);
        }
    }
);

const roomSlice = createSlice({
    name: 'rooms',
    initialState: {
        data: [],
        isLoading: false,
        error: null
    },
    reducers: {
        addRoom: (state, action) => {
            state.data.push(action.payload);
        }
    },
    extraReducers: (builder) => {
        builder
            .addCase(fetchRooms.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(fetchRooms.fulfilled, (state, action) => {
                state.isLoading = false;
                state.data = action.payload;
            })
            .addCase(fetchRooms.rejected, (state, action) => {
                state.isLoading = false;
                state.error = action.error.message;
            });
        // You can add more cases here if needed
    },
});

export default roomSlice.reducer;
