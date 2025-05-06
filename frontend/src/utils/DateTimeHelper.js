export const currentDateTime = () => {
    const now = new Date();
    const pad = (n) => n.toString().padStart(2, '0');

    const year = now.getFullYear();
    const month = pad(now.getMonth() + 1);
    const day = pad(now.getDate());
    const hours = pad(now.getHours());
    const minutes = pad(now.getMinutes());

    const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
    return formattedDateTime;
};

export const appendTimezoneOffset = (dateTimeLocalStr) => {
    const now = new Date();
    const offsetMinutes = -now.getTimezoneOffset();
    const sign = offsetMinutes >= 0 ? '+' : '-';
    const pad = (n) => String(Math.floor(Math.abs(n))).padStart(2, '0');
    const hours = pad(offsetMinutes / 60);
    const minutes = pad(offsetMinutes % 60);
    const timezoneOffset = `${sign}${hours}:${minutes}`;
    console.log(timezoneOffset);

    return dateTimeLocalStr + timezoneOffset;
};
