import { configureStore } from '@reduxjs/toolkit';
import roomsReducer from 'components/RoomList/roomSlice';

export const store = configureStore({
    reducer: {
        rooms: roomsReducer,
        // Add other reducers here as needed
    },
});

export default store;
