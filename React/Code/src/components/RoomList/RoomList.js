import React, { useEffect, useState } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { fetchRooms, createRoom } from './roomSlice';

const RoomList = () => {
    const [roomName, setRoomName] = useState('');
    const dispatch = useDispatch();
    const rooms = useSelector((state) => state.rooms.data);
    const isLoading = useSelector((state) => state.rooms.isLoading);

    useEffect(() => {
        dispatch(fetchRooms());
    }, [dispatch]);

    const handleSubmit = (event) => {
        event.preventDefault();
        dispatch(createRoom(roomName));
        setRoomName("");
    };

    if (isLoading) return <div>Loading...</div>;

    return (
        <div>
            <div>
                {rooms.map(room => (
                    <div key={room.id}>{room.room_name}</div>
                ))}
            </div>
            <form onSubmit={handleSubmit}>
                New Room Name:<input type="text"
                                     value={roomName}
                                     onChange={(e) => setRoomName(e.target.value)}
                                     placeholder="Room Name" />
                <button type="submit">Create Room</button>
            </form>

        </div>
    );
};

export default RoomList;
